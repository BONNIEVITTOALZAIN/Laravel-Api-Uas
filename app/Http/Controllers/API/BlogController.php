<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::all();
        return response()->json([
            'success' => true,
            'data' => $blogs,
        ]);
    }

    public function show($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $blog,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $uploadedFile = Cloudinary::upload($request->file('image')->getRealPath(), [
                'folder' => 'upload/blogs',
            ]);

            $imagePath = $uploadedFile->getSecurePath();
        }

        $blog = Blog::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image' => $imagePath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Blog created successfully.',
            'data' => $blog,
        ]);
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found.',
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Hapus gambar lama dari Cloudinary jika ada
            if ($blog->image) {
                $publicId = pathinfo($blog->image, PATHINFO_FILENAME);
                Cloudinary::destroy("upload/blogs/{$publicId}");
            }

            // Unggah gambar baru ke Cloudinary
            $uploadedFile = Cloudinary::upload($request->file('image')->getRealPath(), [
                'folder' => 'upload/blogs',
            ]);

            $blog->image = $uploadedFile->getSecurePath();
        }

        // Update data lainnya
        $blog->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image' => $blog->image, // Update gambar jika diunggah ulang
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Blog updated successfully.',
            'data' => $blog,
        ]);
    }

    public function destroy($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found.',
            ], 404);
        }

        // Hapus gambar dari Cloudinary jika ada
        if ($blog->image) {
            $publicId = pathinfo($blog->image, PATHINFO_FILENAME);
            Cloudinary::destroy("upload/blogs/{$publicId}");
        }

        $blog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Blog deleted successfully.',
        ]);
    }
}
