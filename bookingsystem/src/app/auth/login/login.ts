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
  styleUrls: ['./login.css']  // исправлено с styleUrl
})
export class Login {
  userName = '';
  password = '';
  errorMessage = '';

  constructor(private auth: Auth, private router: Router, private cdr: ChangeDetectorRef) {}

  login() {
    this.auth.login({ userName: this.userName, password: this.password }).subscribe({
      next: res => {
        if (res.success) {
          this.auth.setAuth(true);
          localStorage.setItem('userName', this.userName);  // исправлено на 'userName'
          this.router.navigate(['/booking']);  // проверь правильный путь
          this.cdr.detectChanges();
        } else {
          this.errorMessage = res.message;
          this.cdr.detectChanges();
        }
      },
      error: () => {
        this.errorMessage = 'Ошибка сервера при входе.';
        this.cdr.detectChanges();
      }
    });
  }
}

