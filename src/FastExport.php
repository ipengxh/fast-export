<?php

namespace FastExport;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FastExport
{
    public static function test()
    {
        echo "run";
    }

    /**
     * @param Model|Builder $builder
     * @param array $headers
     * @param string $fileName
     * @param Closure|null $callback
     */
    public static function download($builder, array $headers, string $fileName, Closure $callback = null)
    {
        $sql = self::parseBuilderToSql($builder);
        $conn = mysqli_connect(env('DB_HOST'), env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_DATABASE'), env('DB_PORT'));
        mysqli_set_charset($conn, 'utf8mb4');
        mysqli_query($conn, "SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
        $result = mysqli_query($conn, $sql, MYSQLI_USE_RESULT);
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename={$fileName}.csv");
        $fp = fopen('php://output', 'w');
        fwrite($fp, "\xEF\xBB\xBF");
        fputcsv($fp, array_values($headers));
        ob_flush();
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                if ($callback) {
                    $csvRow = $callback($row);
                } else {
                    $csvRow = [];
                    foreach ($headers as $key => $header) {
                        $csvRow[] = $row[$key] ?? '';
                    }
                }
                fputcsv($fp, $csvRow);
                ob_flush();
            }
        }
        fclose($fp);
    }

    public static function save($builder, array $headers, string $filePath, Closure $callback = null)
    {
        $sql = self::parseBuilderToSql($builder);
        $conn = mysqli_connect(env('DB_HOST'), env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_DATABASE'), env('DB_PORT'));
        mysqli_set_charset($conn, 'utf8mb4');
        mysqli_query($conn, "SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
        $result = mysqli_query($conn, $sql, MYSQLI_USE_RESULT);
        $fp = fopen($filePath, 'w');
        fwrite($fp, "\xEF\xBB\xBF");
        fputcsv($fp, array_values($headers));
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                if ($callback) {
                    $csvRow = $callback($row);
                } else {
                    $csvRow = [];
                    foreach ($headers as $key => $header) {
                        $csvRow[] = $row[$key] ?? '';
                    }
                }
                fputs($fp, " ");
                fputcsv($fp, $csvRow);
            }
        }
        fclose($fp);
    }

    private static function parseBuilderToSql(Builder $builder)
    {
        $sql = $builder->toSql();
        $bindings = $builder->getBindings();
        foreach ($bindings as $binding) {
            if (is_string($binding)) {
                $replace = "'{$binding}'";
            } else {
                $replace = $binding;
            }
            $sql = Str::replaceFirst('?', $replace, $sql);
        }
        if (!Str::endsWith($sql, ';')) {
            $sql .= ';';
        }
        return $sql;
    }
}
