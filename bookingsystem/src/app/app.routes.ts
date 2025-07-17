import { Routes } from '@angular/router';
import { BookingComponent } from './bookingcomponent/booking.component';
import { About } from './about/about'; // Класс About

export const routes: Routes = [
  { path: 'booking', component: BookingComponent },
  // { path: 'add', component: AddReservationComponent },
  { path: 'about', component: About },
  { path: '', redirectTo: '/booking', pathMatch: 'full' },  // редирект на booking
  { path: '**', redirectTo: '/booking' }  // catch-all
];
