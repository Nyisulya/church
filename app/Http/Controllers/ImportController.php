<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function showForm(): View
    {
        return view('members.import');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,xlsx,xls',
        ]);

        $file = $request->file('csv_file');

        try {
            // Use Maatwebsite Excel to read the file (handles CSV, XLSX, XLS)
            $array = Excel::toArray([], $file);
            
            if (empty($array) || empty($array[0])) {
                throw new \Exception('The file is empty or could not be read.');
            }

            $data = $array[0]; // Get the first sheet
            
            // Remove header row
            $header = array_shift($data);
            
            // Normalize headers to lower case and trim
            $header = array_map(function($h) {
                return trim(strtolower($h));
            }, $header);

            // Map header to index
            $headerMap = array_flip($header);
            
            // Expected headers: name, email, password, confirm_password
            $requiredHeaders = ['name', 'email', 'password'];
            foreach ($requiredHeaders as $req) {
                if (!isset($headerMap[$req])) {
                    throw new \Exception("Missing required header: $req");
                }
            }
            
            DB::beginTransaction();
            
            foreach ($data as $row) {
                // Skip empty rows
                if (empty($row) || !isset($row[$headerMap['email']])) continue;

                $email = $row[$headerMap['email']];
                
                if (!$email || User::where('email', $email)->exists()) {
                    continue; // Skip existing users
                }

                // Create User
                $user = User::create([
                    'name' => $row[$headerMap['name']] ?? 'Unknown',
                    'email' => $email,
                    'password' => Hash::make($row[$headerMap['password']] ?? 'password123'),
                ]);

                // Assign Role (Default to 'member')
                $user->assignRole('member');
            }
            
            DB::commit();
            return redirect()->route('users.index')->with('status', 'Users imported successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['csv_file' => 'Error importing file: ' . $e->getMessage()]);
        }
    }
}
