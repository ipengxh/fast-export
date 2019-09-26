# fast-export

## installation
 ```bash
 composer require ipengxh/fast-export
 ```
 
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
    ], 'users');
    
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
