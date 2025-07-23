import { Routes } from '@angular/router';
import { BookingComponent } from './bookingcomponent/booking.component';
import { About } from './about/about'; // Класс About
import { Addbooking } from './addbooking/addbooking';
import { UpdatebookingComponent } from './updatebooking/updatebooking';

import { Login } from './auth/login/login';
import { Register } from './auth/register/register';
import { AuthGuard } from './guards/auth-guard';

export const routes: Routes = [
  { path: 'booking', component: BookingComponent, canActivate: [AuthGuard] },
  { path: 'add', component: Addbooking, canActivate: [AuthGuard] },
  { path: 'about', component: About },
  { path: 'edit/:id', component: UpdatebookingComponent, canActivate: [AuthGuard] },
  { path: 'login', component: Login },
  { path: 'register', component: Register },

  { path: '', redirectTo: '/booking', pathMatch: 'full' },
  { path: '**', redirectTo: '/booking' }
];
