<?php

namespace App\Services\reset;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseResetService
{
    protected $excludedTables = [
        'industries',
        'migrations',
        'settings',
        'departments',
        'roles',
        'business_hours',
        'permission_role',
        'permissions',
        'role_user',
        'statuses'
    ];



    public function resetDatabase()
    {
        $superUser = Auth::user();

        if (!$superUser || $superUser->email !== 'admin@admin.com') {
            return ['status' => 'error', 'message' => 'Vous n\'avez pas les permissions nécessaires.'];
        }

        $adminId = $superUser->id;

        try {
            // Désactiver les contraintes de clés étrangères
            Schema::disableForeignKeyConstraints();

            $tables = DB::select('SHOW TABLES');
            $tables = array_map('current', $tables);

            foreach ($tables as $table) {
                if ($table === 'users') {

                    DB::table($table)->where('id', '!=', $adminId)->delete();
                } elseif ($table === 'department_user' || $table === 'role_user') {
                    DB::table($table)->where('user_id', '!=', $adminId)->delete();
                } elseif (!in_array($table, $this->excludedTables)) {
                    DB::table($table)->truncate();
                }
            }

            // Réactiver les contraintes de clés étrangères
            Schema::enableForeignKeyConstraints();

            return ['status' => 'success', 'message' => 'Base de données réinitialisée avec succès !'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Erreur : ' . $e->getMessage()];
        }
    }
}
