import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { Auth } from '../../services/auth'; // Убедись, что путь верный
import { HttpClientModule } from '@angular/common/http';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [HttpClientModule, CommonModule, FormsModule, RouterModule],
  templateUrl: './register.html',
  styleUrls: ['./register.css']
})
export class Register {
  userName = '';
  password = '';
  emailAddress = '';
  errorMessage = '';
  successMessage = '';

  constructor(private auth: Auth, private router: Router) {}

  register(): void {
    const trimmedUsername = this.userName.trim();
    const trimmedPassword = this.password.trim();
    const trimmedEmail = this.emailAddress.trim();

    if (!trimmedUsername || !trimmedPassword || !trimmedEmail) {
      this.errorMessage = 'Все поля обязательны.';
      this.successMessage = '';
      return;
    }

    this.auth.register({
      userName: trimmedUsername,
      password: trimmedPassword,
      emailAddress: trimmedEmail
    }).subscribe({
      next: res => {
        if (res.success) {
          this.successMessage = 'Регистрация прошла успешно.';
          this.errorMessage = '';
          setTimeout(() => this.router.navigate(['/login']), 1500);
        } else {
          this.errorMessage = res.message || 'Ошибка регистрации.';
          this.successMessage = '';
        }
      },
      error: err => {
        console.error('Registration error:', err);
        this.errorMessage = 'Ошибка сервера при регистрации.';
        this.successMessage = '';
      }
    });
  }
}
