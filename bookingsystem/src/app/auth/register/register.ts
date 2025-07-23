import { Component } from '@angular/core';
import { Auth } from '../../services/auth';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
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
  email = '';
  password = '';
  confirmPassword = '';
  errorMessage = '';
  successMessage = '';

  constructor(private auth: Auth, private router: Router) {}

  register() {
    const trimmedUserName = this.userName.trim();
    const trimmedEmail = this.email.trim();
    const trimmedPassword = this.password.trim();
    const trimmedConfirm = this.confirmPassword.trim();

    if (!trimmedUserName || !trimmedEmail || !trimmedPassword || !trimmedConfirm) {
      this.errorMessage = '⚠️ Все поля обязательны';
      this.successMessage = '';
      return;
    }

    if (trimmedPassword !== trimmedConfirm) {
      this.errorMessage = '❗ Пароли не совпадают';
      this.successMessage = '';
      return;
    }

    this.auth.register({ userName: trimmedUserName, email: trimmedEmail, password: trimmedPassword }).subscribe({
      next: (res: any) => {
        if (res.success) {
          this.successMessage = '✅ Успешная регистрация. Перенаправление...';
          this.errorMessage = '';
          setTimeout(() => this.router.navigate(['/login']), 1500);
        } else {
          this.errorMessage = res.message || 'Ошибка регистрации';
          this.successMessage = '';
        }
      },
      error: () => {
        this.errorMessage = '❌ Ошибка сервера при регистрации';
        this.successMessage = '';
      }
    });
  }
}
