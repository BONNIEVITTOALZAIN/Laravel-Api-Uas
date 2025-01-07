<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

    public function index2()
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

        // Hapus
        if ($blog->image) {
            // if ($blog->image && Storage::exists('public/' . $blog->image)) {
            //     Storage::delete('public/' . $blog->image);
            // }

            $file_url = $blog->image;
            $publicId = substr($file_url, strrpos($file_url, 'upload/blogs/'), strrpos($file_url, '.') - strrpos($file_url, 'upload/blogs/'));
            // $blog->image = $request->file('image')->store('blog_images', 'public');

            Cloudinary::destroy($publicId);
        }

        if ($request->hasFile('image')) {
            $uploadedFile = Cloudinary::upload($request->file('image')->getRealPath(), [
                'folder' => 'upload/blogs',
            ]);

            $imagePath = $uploadedFile->getSecurePath();
        }

        $blog->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image' => $blog->image,
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
        } else {
            $file_url = $blog->image;
        }

        if ($blog->image) {
            $publicId = substr($file_url, strrpos($file_url, 'upload/blogs/'), strrpos($file_url, '.') - strrpos($file_url, 'upload/blogs/'));

            Cloudinary::destroy($publicId);
        }

        $blog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Blog deleted successfully.',
        ]);
    }
}
