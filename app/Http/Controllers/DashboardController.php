<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::id()) {
            $userType = Auth()->user()->user_type;
            if ($userType == 'Admin') {
                return view('admin_dashboard.admin_home');
            } else if ($userType == 'Employee') {
                return view('employee_dashboard.employee_home');
            } else {
                return redirect()->back();
            }
        }
    }
}