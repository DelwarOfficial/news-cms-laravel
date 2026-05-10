# Admin Dashboard - Developer Quick Start Guide

## Quick Reference for New Developers

### Authorization Patterns

#### Using Policies in Controllers
```php
// Check if user can view any posts
$this->authorize('viewAny', Post::class);

// Check if user can update specific post
$this->authorize('update', $post);

// Check if user can delete specific post
$this->authorize('delete', $post);
```

#### Role-Based Authorization
```php
// Check specific roles
if ($user->hasRole(['Super Admin', 'Admin'])) {
    // Allow action
}

// Check single role
if ($user->hasRole('Editor')) {
    // Allow action
}

// Check has any permission
if ($user->hasAnyPermission(['posts.create', 'posts.edit'])) {
    // Allow action
}
```

### Validation Patterns

#### Using Form Requests
```php
// In controller method
public function store(StorePostRequest $request)
{
    $validated = $request->validated();
    // $validated contains only validated data
}

// Custom validation messages
public function messages(): array
{
    return [
        'title.unique' => 'A post with this title already exists.',
        'password.regex' => 'Password must contain uppercase, lowercase, and numbers.',
    ];
}
```

#### Custom Validation Rules
```php
// File type validation
'file' => 'required|file|mimetypes:image/jpeg,image/png'

// Strong password
'password' => 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/'

// Unique with exclusion
'email' => 'unique:users,email,' . $user->id

// Conditional validation
'code' => 'required_if:type,code'
```

### Common Controller Patterns

#### Create Resource
```php
public function store(StorePostRequest $request)
{
    $this->authorize('create', Post::class);
    
    $validated = $request->validated();
    
    try {
        $post = Post::create($validated);
        
        return redirect()->route('admin.posts.index')
            ->with('success', 'Post created successfully!');
    } catch (\Exception $e) {
        Log::error('Post creation failed', ['error' => $e->getMessage()]);
        return back()->with('error', 'Failed to create post');
    }
}
```

#### Update Resource
```php
public function update(Request $request, Post $post)
{
    $this->authorize('update', $post);
    
    $validated = $request->validate([...]);
    
    try {
        $post->update($validated);
        return back()->with('success', 'Updated!');
    } catch (\Exception $e) {
        Log::error('Update failed', ['error' => $e->getMessage()]);
        return back()->with('error', 'Failed to update');
    }
}
```

#### Delete Resource
```php
public function destroy(Post $post)
{
    $this->authorize('delete', $post);
    
    // Check for constraints
    if ($post->comments()->count() > 0) {
        return back()->with('error', 'Cannot delete post with comments');
    }
    
    try {
        $post->delete();
        return back()->with('success', 'Deleted successfully!');
    } catch (\Exception $e) {
        return back()->with('error', 'Failed to delete');
    }
}
```

### API Response Patterns

#### Success Response
```php
return response()->json([
    'status' => 'success',
    'message' => 'Operation completed successfully',
    'data' => $resource->load('relationships')
]);
```

#### Error Response
```php
return response()->json([
    'status' => 'error',
    'message' => 'Descriptive error message'
], 500);

// Unauthorized
return response()->json(['message' => 'Unauthorized'], 403);

// Validation error
return response()->json([
    'status' => 'error',
    'message' => 'Validation failed',
    'errors' => $validator->errors()
], 422);
```

### Query Optimization

#### Eager Load Relationships
```php
// Good - Prevents N+1 queries
$posts = Post::with('author', 'categories', 'comments')->get();

// Bad - N+1 query problem
$posts = Post::all();
foreach ($posts as $post) {
    echo $post->author->name; // Query per post!
}
```

#### Pagination
```php
$posts = Post::with('author')
    ->latest()
    ->paginate(20)
    ->withQueryString(); // Preserve query parameters
```

#### Selective Fields
```php
// Load only needed fields
$posts = Post::select(['id', 'title', 'user_id'])
    ->with('author:id,name')
    ->get();
```

### Testing Patterns

#### Testing Authorization
```php
public function test_author_cannot_edit_others_post(): void
{
    $otherPost = Post::factory()->create(['user_id' => $this->admin->id]);
    
    $response = $this->actingAs($this->author)
        ->post(route('admin.posts.update', $otherPost), [...]);
    
    $response->assertStatus(403);
}
```

#### Testing Validation
```php
public function test_post_validation_on_store(): void
{
    $response = $this->actingAs($this->admin)
        ->post(route('admin.posts.store'), [
            'title' => '',
            'content' => '',
            'status' => 'invalid',
        ]);
    
    $response->assertSessionHasErrors(['title', 'content', 'status']);
}
```

#### Testing File Upload
```php
public function test_admin_can_upload_media(): void
{
    Storage::fake('public');
    $file = UploadedFile::fake()->image('test.jpg');
    
    $response = $this->actingAs($this->admin)
        ->post(route('admin.media.store'), ['file' => $file]);
    
    $response->assertSessionHas('success');
    $this->assertDatabaseCount('media', 1);
}
```

### Common Mistakes to Avoid

#### ❌ Forgetting Authorization
```php
// Bad - No authorization check!
public function destroy(Post $post)
{
    $post->delete();
    return back();
}

// Good
public function destroy(Post $post)
{
    $this->authorize('delete', $post);
    $post->delete();
    return back()->with('success', 'Deleted!');
}
```

#### ❌ Not Wrapping File Operations
```php
// Bad - No error handling
$path = $file->store('media', 'public');
Storage::disk('public')->delete($path);

// Good
try {
    $path = $file->store('media', 'public');
    Storage::disk('public')->delete($path);
} catch (\Exception $e) {
    Log::error('File operation failed', ['error' => $e->getMessage()]);
    return back()->with('error', 'Operation failed');
}
```

#### ❌ N+1 Query Problem
```php
// Bad
$posts = Post::all();
foreach ($posts as $post) {
    echo $post->author->name; // Query per post
}

// Good
$posts = Post::with('author')->get();
foreach ($posts as $post) {
    echo $post->author->name; // One query total
}
```

#### ❌ Forgetting Validation
```php
// Bad
$post = Post::create($request->all());

// Good - Use Form Request or validate
$validated = $request->validate([...]);
$post = Post::create($validated);
```

### Debugging Tips

#### Enable Query Logging
```php
// In bootstrap/app.php or controller
\Illuminate\Support\Facades\DB::enableQueryLog();

// Later
dd(\Illuminate\Support\Facades\DB::getQueryLog());
```

#### Log User Actions
```php
Log::info('User updated post', [
    'user_id' => auth()->id(),
    'post_id' => $post->id,
    'changes' => $post->getChanges()
]);
```

#### Check Authorization Directly
```php
// In tinker or test
$user = User::find(1);
$post = Post::find(1);
dd($user->can('update', $post));
```

### File Permissions

```bash
# Make storage writable
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# Make public/uploads writable for media
chmod -R 775 public/storage/
```

### Common Routes

```
Admin Dashboard: /admin/
Posts: /admin/posts
Users: /admin/users
Media: /admin/media
Categories: /admin/categories
Comments: /admin/comments
Settings: /admin/settings
Widgets: /admin/widgets
Advertisements: /admin/advertisements

API:
Auth: /api/auth/login, /api/auth/logout, /api/auth/me
Admin Posts: /api/admin/posts
Admin Media: /api/admin/media
Admin Comments: /api/admin/comments
```

### Performance Best Practices

1. **Use Pagination**: Don't load all records
   ```php
   $posts = Post::paginate(20);
   ```

2. **Eager Load**: Load relationships with main query
   ```php
   $posts = Post::with('author', 'categories')->get();
   ```

3. **Cache Dashboard Stats**: Cache expensive queries
   ```php
   $stats = Cache::remember('stats', 300, function() {
       return [...];
   });
   ```

4. **Index Database Columns**: Add indexes to frequently queried columns
   ```php
   $table->string('slug')->unique()->index();
   ```

5. **Selective Fields**: Only fetch needed columns
   ```php
   Post::select(['id', 'title', 'user_id'])->get();
   ```

---

**For more details, see ADMIN_DASHBOARD_IMPROVEMENTS.md**
