<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Support\AdminTableSort;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        
        if (!in_array($status, ['pending', 'approved', 'rejected', 'spam'])) {
            $status = 'pending';
        }

        $allowedSorts = ['created_at', 'updated_at', 'status', 'author_name', 'author_email'];
        [$sortBy, $sortDirection] = AdminTableSort::resolve($request, $allowedSorts);
        
        $comments = AdminTableSort::apply(
            Comment::with('post', 'user')->where('status', $status),
            $allowedSorts,
            $sortBy,
            $sortDirection
        )->paginate(20)->withQueryString();

        $stats = [
            'pending' => Comment::where('status', 'pending')->count(),
            'approved' => Comment::where('status', 'approved')->count(),
            'rejected' => Comment::where('status', 'rejected')->count(),
            'spam' => Comment::where('status', 'spam')->count(),
        ];

        return view('admin.comments.index', compact('comments', 'status', 'stats', 'sortBy', 'sortDirection'));
    }

    public function approve(Comment $comment)
    {
        $this->authorize('update', $comment);
        
        $comment->update(['status' => 'approved']);
        return back()->with('success', 'Comment approved successfully!');
    }

    public function reject(Comment $comment)
    {
        $this->authorize('update', $comment);
        
        $comment->update(['status' => 'rejected']);
        return back()->with('success', 'Comment rejected successfully!');
    }

    public function markSpam(Comment $comment)
    {
        $this->authorize('update', $comment);
        
        $comment->update(['status' => 'spam']);
        return back()->with('success', 'Comment marked as spam!');
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        
        $comment->delete();
        return back()->with('success', 'Comment deleted successfully!');
    }
}
