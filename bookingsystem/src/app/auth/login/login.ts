import { Component, ChangeDetectorRef } from '@angular/core';
import { Auth } from '../../services/auth';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [HttpClientModule, CommonModule, FormsModule, RouterModule],
  templateUrl: './login.html',
  styleUrls: ['./login.css']
})
export class Login {
  userName = '';
  password = '';
  errorMessage = '';
  waitTime: number = 0;
  countdownInterval: any;

  constructor(private auth: Auth, private router: Router, private cdr: ChangeDetectorRef) {}

  login() {
    if (this.waitTime > 0) {
      console.log('Login blocked. Wait time:', this.waitTime);
      return;
    }

    this.auth.login({ userName: this.userName, password: this.password }).subscribe({
      next: res => {
        console.log('Response from server:', res);

        if (res.success) {
          this.errorMessage = '';
          this.auth.setAuth(true);
          localStorage.setItem('userName', this.userName);
          this.router.navigate(['/booking']);
        } else {
          this.errorMessage = res.message || 'Ошибка входа';

          if (res.remainingAttempts !== undefined) {
            this.errorMessage += ` Осталось попыток: ${res.remainingAttempts}`;
          }

          if (res.waitTime) {
            this.waitTime = res.waitTime;
            this.startCountdown();
          }
        }

        this.cdr.detectChanges();
      },
      error: err => {
        console.error('Server error:', err);
        this.errorMessage = 'Ошибка сервера при входе.';
        this.cdr.detectChanges();
      }
    });
  }

  startCountdown() {
    if (this.countdownInterval) {
      clearInterval(this.countdownInterval);
    }

    this.countdownInterval = setInterval(() => {
      this.waitTime--;
      this.cdr.detectChanges();

      if (this.waitTime <= 0) {
        clearInterval(this.countdownInterval);
      }
    }, 1000);
  }
}
