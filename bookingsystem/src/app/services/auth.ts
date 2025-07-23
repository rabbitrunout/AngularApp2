import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { tap } from 'rxjs/operators';
import { Observable } from 'rxjs';

// Тип ответа от сервера для логина и регистрации
export interface AuthResponse {
  success: boolean;
  message?: string;
}

@Injectable({ providedIn: 'root' })
export class Auth {
  private baseUrl = 'http://localhost/backend'; // URL к твоему PHP-бэкенду

  isAuthenticated = false;

  constructor(private http: HttpClient, private router: Router) {}

  login(credentials: { email: string; password: string }): Observable<AuthResponse> {
    return this.http.post<AuthResponse>(`${this.baseUrl}/login.php`, credentials, { withCredentials: true })
      .pipe(
        tap(res => {
          if (res.success) {
            this.isAuthenticated = true;
          }
        })
      );
  }

  // Явно можно установить состояние аутентификации
  setAuth(status: boolean) {
    this.isAuthenticated = status;
  }

  register(data: { userName?: string; email: string; password: string }): Observable<AuthResponse> {
    return this.http.post<AuthResponse>(`${this.baseUrl}/register.php`, data);
  }

  checkAuth(): Observable<any> {
    return this.http.get(`${this.baseUrl}/checkAuth.php`, { withCredentials: true });
  }

  logout(): void {
    this.http.get(`${this.baseUrl}/logout.php`, { withCredentials: true }).subscribe(() => {
      this.isAuthenticated = false;
      this.router.navigate(['/login']);
    });
  }
}
