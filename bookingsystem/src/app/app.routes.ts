import { Routes } from '@angular/router';
import { About } from './about/about';
import { BookingComponent } from './bookingcomponent/booking.component';
import { Addbooking } from './addbooking/addbooking';
import { UpdatebookingComponent } from './updatebooking/updatebooking';
import { Login } from './auth/login/login';
import { Register } from './auth/register/register';
import { authGuard } from './guards/auth-guard';

export const routes: Routes = [
  { path: 'reservations', component: BookingComponent, canActivate: [authGuard] },
  { path: 'add', component: Addbooking, canActivate: [authGuard] },
  { path: 'edit/:id', component: UpdatebookingComponent, canActivate: [authGuard] },
  { path: 'about', component: About },
  { path: 'login', component: Login },
  { path: 'register', component: Register },

  { path: '', redirectTo: '/reservations', pathMatch: 'full' },
  { path: '**', redirectTo: '/reservations' }
];
