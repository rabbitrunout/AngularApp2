import { Routes } from '@angular/router';
import { BookingComponent } from './bookingcomponent/booking.component';
import { About } from './about/about'; // Класс About
import { Addbooking } from './addbooking/addbooking';
import { UpdatebookingComponent } from './updatebooking/updatebooking';

export const routes: Routes = [
  { path: 'booking', component: BookingComponent },
  // { path: 'add', component: AddReservationComponent },
  { path: 'about', component: About },
  { path: 'add', component: Addbooking },
  { path: 'updatebooking/:id', loadComponent: () => import('./updatebooking/updatebooking').then(m => m.UpdatebookingComponent)},
  { path: '', redirectTo: '/booking', pathMatch: 'full' },  // редирект на booking
  { path: '**', redirectTo: '/booking' }  // catch-all
];
