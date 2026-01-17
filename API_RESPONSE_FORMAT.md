# API Response Standardization

## Overview
All API responses in this application now follow a unified format with `status`, `message`, and `data` fields for consistency and better client-side handling.

## Response Format

### Success Response
```json
{
    "status": "success",
    "message": "Descriptive success message",
    "data": {
        // Response data (optional)
    }
}
```

### Error Response
```json
{
    "status": "failed",
    "message": "Descriptive error message",
    "errors": {
        // Validation errors or additional error details (optional)
    }
}
```

## HTTP Status Codes

- **200 OK**: Successful GET, PUT, PATCH requests
- **201 Created**: Successful POST requests that create resources
- **204 No Content**: Successful DELETE requests (now returns 200 with success message)
- **400 Bad Request**: General client errors
- **401 Unauthorized**: Authentication required or failed
- **403 Forbidden**: Authenticated but not authorized
- **404 Not Found**: Resource not found
- **422 Unprocessable Entity**: Validation errors

## Using the ApiResponse Trait

All controllers extend the base `Controller` class which includes the `ApiResponse` trait. This provides the following helper methods:

### Success Responses

#### `successResponse($data = null, string $message = 'Success', int $statusCode = 200)`
General success response.

```php
return $this->successResponse($users, 'Users retrieved successfully');
```

#### `createdResponse($data = null, string $message = 'Resource created successfully')`
For newly created resources (201 status).

```php
return $this->createdResponse($user, 'User created successfully');
```

#### `noContentResponse(string $message = 'Resource deleted successfully')`
For successful deletions (returns 200 with message instead of 204).

```php
return $this->noContentResponse('User deleted successfully');
```

### Error Responses

#### `errorResponse(string $message = 'Error', int $statusCode = 400, $errors = null)`
General error response.

```php
return $this->errorResponse('Something went wrong', 500);
```

#### `unauthorizedResponse(string $message = 'Unauthorized')`
For authentication failures (401 status).

```php
return $this->unauthorizedResponse('Invalid credentials');
```

#### `forbiddenResponse(string $message = 'Forbidden')`
For authorization failures (403 status).

```php
return $this->forbiddenResponse('You do not have permission to access this resource');
```

#### `notFoundResponse(string $message = 'Resource not found')`
For missing resources (404 status).

```php
return $this->notFoundResponse('User not found');
```

#### `validationErrorResponse($errors, string $message = 'Validation Error')`
For validation failures (422 status).

```php
return $this->validationErrorResponse($validator->errors(), 'Validation failed');
```

## Examples

### Index (List) Endpoint
```php
public function index()
{
    $users = User::all();
    return $this->successResponse($users, 'Users retrieved successfully');
}
```

**Response:**
```json
{
    "status": "success",
    "message": "Users retrieved successfully",
    "data": [...]
}
```

### Store (Create) Endpoint
```php
public function store(CreateUserRequest $request)
{
    $user = User::create($request->validated());
    return $this->createdResponse($user, 'User created successfully');
}
```

**Response:**
```json
{
    "status": "success",
    "message": "User created successfully",
    "data": {...}
}
```

### Update Endpoint
```php
public function update(UpdateUserRequest $request, string $id)
{
    $user = User::findOrFail($id);
    $user->update($request->validated());
    return $this->successResponse($user, 'User updated successfully');
}
```

**Response:**
```json
{
    "status": "success",
    "message": "User updated successfully",
    "data": {...}
}
```

### Delete Endpoint
```php
public function destroy(string $id)
{
    $user = User::findOrFail($id);
    $user->delete();
    return $this->noContentResponse('User deleted successfully');
}
```

**Response:**
```json
{
    "status": "success",
    "message": "User deleted successfully"
}
```

### Error Handling
```php
public function login(LoginRequest $request)
{
    $user = User::where('email', $request->email)->first();
    
    if (!$user || !Hash::check($request->password, $user->password)) {
        return $this->unauthorizedResponse('Invalid credentials');
    }
    
    // ... generate token
    
    return $this->successResponse([
        'user' => $user,
        'access_token' => $token,
    ], 'Login successful');
}
```

**Error Response:**
```json
{
    "status": "failed",
    "message": "Invalid credentials"
}
```

**Success Response:**
```json
{
    "status": "success",
    "message": "Login successful",
    "data": {
        "user": {...},
        "access_token": "..."
    }
}
```

## Exception Handling

The `ApiExceptionHandler` automatically formats all exceptions to match this standard:

```json
{
    "status": "failed",
    "message": "Error description",
    "errors": {
        // Validation errors if applicable
    }
}
```

## Migration Notes

All controllers have been updated to use the new standardized response format:
- ✅ UserController
- ✅ AuthController
- ✅ TeamController
- ✅ TeamMemberController
- ✅ TaskController
- ✅ TaskSubmissionController
- ✅ CouncilController
- ✅ CouncilSessionController
- ✅ AttendanceController
- ✅ RoleController
- ✅ CacheController

## Benefits

1. **Consistency**: All responses follow the same structure
2. **Client-side simplification**: Frontend can handle all responses uniformly
3. **Better error handling**: Clear distinction between success and failure
4. **Improved debugging**: Descriptive messages for all operations
5. **Type safety**: Clients can rely on the response structure
