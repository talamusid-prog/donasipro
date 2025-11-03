<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bankAccounts = BankAccount::orderBy('bank_name')->get();
        return view('admin.bank-accounts.index', compact('bankAccounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.bank-accounts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'logo' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'account_number' => 'required|string|max:50',
            'account_holder' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $data = [
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_holder' => $request->account_holder,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            try {
                $logo = $request->file('logo');
                $logoName = time() . '_' . $logo->getClientOriginalName();
                $logoPath = 'bank-logos/' . $logoName;
                $success = \Storage::disk('public')->put($logoPath, file_get_contents($logo->getRealPath()));
                
                if ($success) {
                    $data['logo'] = 'public/' . $logoPath;
                } else {
                    throw new \Exception('Failed to store file');
                }
            } catch (\Exception $e) {
                return back()->withErrors(['logo' => 'Gagal mengupload logo: ' . $e->getMessage()]);
            }
        }

        BankAccount::create($data);

        return redirect()->route('admin.bank-accounts.index')
                        ->with('success', 'Rekening bank berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BankAccount $bankAccount)
    {
        return view('admin.bank-accounts.show', compact('bankAccount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BankAccount $bankAccount)
    {
        return view('admin.bank-accounts.edit', compact('bankAccount'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'logo' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'account_number' => 'required|string|max:50',
            'account_holder' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $data = [
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_holder' => $request->account_holder,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($bankAccount->logo && \Storage::exists($bankAccount->logo)) {
                \Storage::delete($bankAccount->logo);
            }
            
            try {
                $logo = $request->file('logo');
                $logoName = time() . '_' . $logo->getClientOriginalName();
                $logoPath = 'bank-logos/' . $logoName;
                $success = \Storage::disk('public')->put($logoPath, file_get_contents($logo->getRealPath()));
                
                if ($success) {
                    $data['logo'] = 'public/' . $logoPath;
                } else {
                    throw new \Exception('Failed to store file');
                }
            } catch (\Exception $e) {
                return back()->withErrors(['logo' => 'Gagal mengupload logo: ' . $e->getMessage()]);
            }
        }

        $bankAccount->update($data);

        return redirect()->route('admin.bank-accounts.index')
                        ->with('success', 'Rekening bank berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();

        return redirect()->route('admin.bank-accounts.index')
                        ->with('success', 'Rekening bank berhasil dihapus.');
    }
}
