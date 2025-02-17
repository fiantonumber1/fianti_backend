<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithLimit;

class UsersImport implements ToModel, WithStartRow,WithLimit
{
    /**
     * @param array $row
     *
     * @return User|null
     */
    public function startRow(): int
    {
        return 2;
    }
    public function limit(): int
    {
        return 10000;
    }
    public function model(array $row)
    {
        return new User([
           'name'     => $row[0],
           'email'    => $row[1], 
           'password' => Hash::make($row[2]),
           'username'    => $row[3], 
           'rule'    => $row[4], 
        ]);
    }
}
