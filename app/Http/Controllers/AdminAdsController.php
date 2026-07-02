<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Http\Request;

class AdminAdsController extends Controller
{
    public function index()
    {
        return view('admin.ads', ['ads' => Ad::orderBy('sort_order')->get()]);
    }

    public function store(Request $request)
    {
        $data = $this->validateAd($request, true);

        $data['image']     = $this->storeImage($request->file('image'));
        $data['is_active'] = $request->boolean('is_active');
        $data['expires_at'] = $this->computeExpiry($data['starts_at'] ?? null, $data['duration_days']);

        Ad::create($data);

        return back()->with('status', 'Publicité ajoutée.');
    }

    public function update(Request $request, Ad $ad)
    {
        $data = $this->validateAd($request, false);
        $data['is_active'] = $request->boolean('is_active');
        $data['expires_at'] = $this->computeExpiry($data['starts_at'] ?? null, $data['duration_days']);

        if ($request->hasFile('image')) {
            $data['image'] = $this->storeImage($request->file('image'));
        }

        $ad->update($data);

        return back()->with('status', 'Publicité mise à jour.');
    }

    public function destroy(Ad $ad)
    {
        $ad->delete();
        return back()->with('status', 'Publicité supprimée.');
    }

    private function validateAd(Request $request, bool $imageRequired): array
    {
        $rules = [
            'image'         => [$imageRequired ? 'required' : 'nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'client_name'   => ['nullable', 'string', 'max:100'],
            'price'         => ['nullable', 'integer', 'min:0'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:3650'],
            'starts_at'     => ['nullable', 'date'],
            'contact'       => ['nullable', 'string', 'max:40'],
            'cta_type'      => ['required', 'in:whatsapp,call'],
            'cta_label'     => ['required', 'string', 'max:80'],
            'sort_order'    => ['required', 'integer', 'min:0'],
            'notes'         => ['nullable', 'string', 'max:400'],
        ];

        return $request->validate($rules);
    }

    private function computeExpiry(?string $startsAt, int $days): ?\Carbon\Carbon
    {
        $start = $startsAt ? \Carbon\Carbon::parse($startsAt) : now();
        return $start->copy()->addDays($days);
    }

    private function storeImage(\Illuminate\Http\UploadedFile $file): string
    {
        $name = 'ad-' . now()->timestamp . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('img'), $name);
        return $name;
    }
}
