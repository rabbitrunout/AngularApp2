import { Component, ChangeDetectorRef } from '@angular/core';
import { Auth } from '../../services/auth';
import { Router } from '@angular/router';
import { HttpClientModule } from '@angular/common/http';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';

interface LoginResponse {
  success: boolean;
  message?: string;
}

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [HttpClientModule, CommonModule, FormsModule, RouterModule],
  templateUrl: './login.html',
  styleUrls: ['./login.css']
})
export class Login {
  email = '';
  password = '';
  errorMessage = '';

  constructor(private auth: Auth, private router: Router, private cdr: ChangeDetectorRef) {}

  login() {
    this.auth.login({ email: this.email, password: this.password }).subscribe({
      next: (res: LoginResponse) => {
        if (res.success) {
          this.auth.setAuth(true); // Убедись, что метод есть
          localStorage.setItem('userEmail', this.email);
          this.router.navigate(['/booking']);
          this.cdr.detectChanges();
        } else {
          this.errorMessage = res.message || 'Ошибка входа';
          this.cdr.detectChanges();
        }
      },
      error: () => {
        this.errorMessage = '❌ Неверный email или пароль';
        this.cdr.detectChanges();
      }
    });
  }
}
