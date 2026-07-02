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
        $data = $this->validate($request);

        abort_unless($request->hasFile('image'), 422, 'Image obligatoire.');

        $data['image']     = $this->storeImage($request->file('image'));
        $data['is_active'] = $request->boolean('is_active');

        Ad::create($data);

        return back()->with('status', 'Publicité ajoutée.');
    }

    public function update(Request $request, Ad $ad)
    {
        $data = $this->validate($request);
        $data['is_active'] = $request->boolean('is_active');

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

    private function validate(Request $request): array
    {
        return $request->validate([
            'image'      => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'contact'    => ['nullable', 'string', 'max:40'],
            'cta_type'   => ['required', 'in:whatsapp,call'],
            'cta_label'  => ['required', 'string', 'max:80'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);
    }

    private function storeImage(\Illuminate\Http\UploadedFile $file): string
    {
        $name = 'ad-' . now()->timestamp . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('img'), $name);
        return $name;
    }
}
