<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        Feedback::create($validated);

        return response()->json(['message' => 'Feedback berhasil dikirim!'], 201);
    }

    public function index()
    {
        $feedbacks = Feedback::latest()->get();
        return response()->json($feedbacks, 200);
    }

    public function index2()
    {
        $feedbacks = Feedback::all();
        return response()->json([
            'success' => true,
            'data' => $feedbacks,
        ]);
    }

    public function destroy($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->delete();

        return response()->json(['message' => 'Feedback berhasil dihapus!'], 200);
    }
}
