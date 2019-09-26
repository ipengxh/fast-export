# fast-export

## installation
 ```bash
 composer require ipengxh/fast-export
 ```
 
## benchmark
```sql
create table tests
(
    id         bigint unsigned auto_increment
        primary key,
    string_1   varchar(255) not null,
    string_2   varchar(255) not null,
    string_3   varchar(255) not null,
    string_4   varchar(255) not null,
    string_5   varchar(255) not null,
    integer_1  int          not null,
    integer_2  int          not null,
    integer_3  int          not null,
    integer_4  int          not null,
    integer_5  int          not null,
    created_at timestamp    null,
    updated_at timestamp    null
);
```

1,000,000 rows

```php
$headers = [
    'id' => 'ID',
    'string_1' => "String 1",
    'string_2' => "String 2",
    'string_3' => "String 3",
    'string_4' => "String 4",
    'string_5' => "String 5",
    'integer_1' => "Integer 1",
    'integer_2' => "Integer 2",
    'integer_3' => "Integer 3",
    'integer_4' => "Integer 4",
    'integer_5' => "Integer 5",
];
$data = app(Test::class)->orderBy('id', 'desc');
FastExport::download($data, $headers, 'test data');
```

finished in 2.57 seconds.
 
## usage

```php
use FastExport\FastExport;

public function export()
{
    $users = \App\Models\User::withTrashed()->orderBy('id', 'desc');
    
    // save as csv file
    FastExport::save($users, storage_path("user lists.csv"), [
        'id' => "User ID",
        'name' => "Name of user",
        'email' => "E-mail address",
        'created_at' => "Registered at"
    ]);
    
    // export as csv file
    FastExport::csv($users, [
        'id' => "User ID",
        'name' => "Name of user",
        'email' => "E-mail address",
        'created_at' => "Registered at"
    ], 'users');
    
    // advanced export
    FastExport::csv($users, [
        'id' => "User ID",
        'name' => "Name of user",
        'email' => "E-mail address",
        'created_at' => "Registered at",
        'status' => "Status"
    ], 'users', function ($row) {
        return [
            $row->id,
            $row->name,
            $row->email,
            $row->created_at,
            $row->deleted_at ? "Deleted" : "Actived"
        ];
    });
}
```
