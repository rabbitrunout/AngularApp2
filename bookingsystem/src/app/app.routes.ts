import { Routes } from '@angular/router';
import { BookingComponent } from './bookingcomponent/booking.component';
import { About } from './about/about'; // Класс About

export const routes: Routes = [
  { path: '', component: BookingComponent },
  { path: 'about', component: About },
  { path: "**", redirectTo: "/bookingcomponent"}
];
