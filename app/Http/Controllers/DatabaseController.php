<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DatabaseController extends Controller
{
    public function resetDatabase(): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user(); // Correction ici

        $tables = [
            'absences', 'activities', 'appointments', 'clients', 'comments', 'contacts',
            'documents', 'integrations', 'invoice_lines', 'invoices',
            'leads', 'mails', 'notifications', 'offers', 'password_resets',
            'payments', 'products', 'projects',
            'subscriptions', 'tasks', 'users'
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        foreach ($tables as $table) {
            if ($table !== 'users') {
                DB::table($table)->truncate();
            } else {
                DB::table($table)->where('id', '!=', $user->id)->delete();
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        return redirect()->route('dashboard'); // Correction ici
    }
}

