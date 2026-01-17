# Angular API Integration Guide

This guide details the available API endpoints and provides examples of how to consume them using Angular's `HttpClient`.

## Base Configuration

**Base URL**: `http://localhost:8000/api` (Update this to your production URL)

### Authentication
The API uses JWT (JSON Web Tokens). You must include the `Authorization` header in all protected requests.

**Header Format:**
```
Authorization: Bearer <your_access_token>
```

### Angular Service Example
Create a shared service (e.g., `ApiService`) to handle requests.

```typescript
import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private baseUrl = 'http://localhost:8000/api';

  constructor(private http: HttpClient) {}

  // Helper to get headers with token
  private getHeaders(): HttpHeaders {
    const token = localStorage.getItem('access_token'); // Or get from AuthService
    return new HttpHeaders({
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    });
  }

  // Example generic GET
  get<T>(endpoint: string, params?: any): Observable<T> {
    return this.http.get<T>(`${this.baseUrl}/${endpoint}`, {
      headers: this.getHeaders(),
      params: params
    });
  }

  // Example generic POST
  post<T>(endpoint: string, data: any): Observable<T> {
    return this.http.post<T>(`${this.baseUrl}/${endpoint}`, data, {
      headers: this.getHeaders()
    });
  }
}
```

---

## Endpoints

### 1. Authentication

#### Login
*   **URL**: `/login`
*   **Method**: `POST`
*   **Auth Required**: No
*   **Body**:
    ```json
    {
      "email": "user@example.com",
      "password": "password"
    }
    ```
*   **Angular Call**:
    ```typescript
    login(credentials: {email: string, password: string}) {
      return this.http.post(`${this.baseUrl}/login`, credentials);
    }
    ```

#### Logout
*   **URL**: `/logout`
*   **Method**: `POST`
*   **Auth Required**: Yes
*   **Angular Call**:
    ```typescript
    logout() {
      return this.http.post(`${this.baseUrl}/logout`, {}, { headers: this.getHeaders() });
    }
    ```

---

### 2. Users

#### Get All Users (Paginated)
*   **URL**: `/users`
*   **Method**: `GET`
*   **Params**:
    *   `pageIndex` (optional, default: 1)
    *   `pageSize` (optional, default: 10)
    *   `search` (optional)
*   **Angular Call**:
    ```typescript
    getUsers(pageIndex: number = 1, pageSize: number = 10, search: string = '') {
      return this.http.get(`${this.baseUrl}/users`, {
        headers: this.getHeaders(),
        params: { pageIndex, pageSize, search }
      });
    }
    ```

#### Create User
*   **URL**: `/users`
*   **Method**: `POST`
*   **Body**:
    ```json
    {
      "name": "John Doe",
      "email": "john@example.com",
      "password": "password123",
      "role_id": "uuid-role-id",
      "council_id": "uuid-council-id"
    }
    ```

#### Bulk Create Users
*   **URL**: `/users/bulk`
*   **Method**: `POST`
*   **Body**: `FormData` with a key `file` (Excel/CSV).
*   **Angular Call**:
    ```typescript
    uploadUsers(file: File) {
      const formData = new FormData();
      formData.append('file', file);
      return this.http.post(`${this.baseUrl}/users/bulk`, formData, {
        headers: new HttpHeaders({
          'Authorization': `Bearer ${token}`
          // Content-Type is auto-set by browser for FormData
        })
      });
    }
    ```

---

### 3. Councils

#### Get All Councils
*   **URL**: `/councils`
*   **Method**: `GET`

#### Create Council
*   **URL**: `/councils`
*   **Method**: `POST`
*   **Body**:
    ```json
    {
      "name": "General Council",
      "description": "Main governing body"
    }
    ```

---

### 4. Roles

#### Get Roles
*   **URL**: `/roles`
*   **Method**: `GET`

#### Create Role
*   **URL**: `/roles`
*   **Method**: `POST`
*   **Body**: `{"name": "Admin"}`

---

### 5. Tasks

#### Get Tasks
*   **URL**: `/tasks`
*   **Method**: `GET`
*   **Standard CRUD**: `/tasks/{id}` (GET, PUT, DELETE)

---

### 6. Council Sessions

#### Get Sessions
*   **URL**: `/sessions`
*   **Method**: `GET`

#### Create Session
*   **URL**: `/sessions`
*   **Method**: `POST`
*   **Body**:
    ```json
    {
      "title": "Annual Meeting",
      "date": "2024-01-01 10:00:00",
      "description": "Discussing goals",
      "material": "link-to-material",
      "council_id": "uuid-council-id"
    }
    ```

---

### 7. Attendances

#### Get Attendances
*   **URL**: `/attendances`
*   **Method**: `GET`
*   **Params**: `pageIndex`, `pageSize`

#### Create Attendance (Single)
*   **URL**: `/attendances`
*   **Method**: `POST`
*   **Body**:
    ```json
    {
      "user_id": "uuid-user-id",
      "session_id": "uuid-session-id",
      "status": "Present",
      "date": "2024-01-01"
    }
    ```

#### Bulk Create Attendance
*   **URL**: `/attendances/bulk`
*   **Method**: `POST`
*   **Body**: `FormData` type (file upload).

---

### 8. Notifications

#### Get Notifications
*   **URL**: `/notifications`
*   **Method**: `GET`
*   **Returns**: List of user notifications.

---

### 9. Task Submissions

#### Get Submissions
*   **URL**: `/task-submissions`
*   **Method**: `GET`
*   **Standard CRUD**: `/task-submissions/{id}`

---

### 10. Cache Management (Admin)
*   `GET /cache/stats`
*   `DELETE /cache/endpoint`
*   `DELETE /cache/resource`
*   `DELETE /cache/user/{userId}`
