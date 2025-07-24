import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';

@Injectable({ providedIn: 'root' })
export class Auth {
  private baseUrl = 'http://localhost/angularapp2/bookingapi/';
  isAuthenticated = false;

  constructor(private http: HttpClient, private router: Router) {}

  login(user: any) {
    return this.http.post<any>(`${this.baseUrl}login.php`, user);
  }

  register(user: any) {
    console.log('Sending user to register:', user);
    return this.http.post<any>(`${this.baseUrl}register.php`, user);
  }

  logout() {
    this.http.get(`${this.baseUrl}logout.php`).subscribe(() => {
      this.isAuthenticated = false;
      if (typeof window !== 'undefined' && window.localStorage) {
        localStorage.removeItem('auth');
      }
      this.router.navigate(['/login']);
    });
  }

  checkAuth() {
    return this.http.get<any>(`${this.baseUrl}checkAuth.php`);
  }

  setAuth(auth: boolean) {
    this.isAuthenticated = auth;
    if (typeof window !== 'undefined' && window.localStorage) {
      localStorage.setItem('auth', auth ? 'true' : 'false');
    }
  }

  getAuth(): boolean {
    if (typeof window !== 'undefined' && window.localStorage) {
      return localStorage.getItem('auth') === 'true';
    }
    return false;
  }
}
