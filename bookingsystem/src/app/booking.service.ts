import { Injectable } from "@angular/core";
import { HttpClient, HttpParams } from "@angular/common/http";

import { map } from 'rxjs/operators';

import { BookingItem } from "./bookingItem";

@Injectable({
    providedIn: 'root',
})

export class BookingService {
    baseUrl = 'http://localhost/angularapp2/bookingapi';

    constructor(
        private http: HttpClient
    ){
        // no ststements required
    }

    getAll() {
        return this.http.get(`${this.baseUrl}/list.php`).pipe(
            map((res: any) => {
                return res ['data'];
            })
        );
    }
    add(reservation: BookingItem ) {
    return this.http.post(`${this.baseUrl}/add`, {data: reservation}).pipe(
      map((res: any) => {
        return res['data'];
      })
    );
  }

  edit(reservation: BookingItem ) {
    return this.http.put(`${this.baseUrl}/edit`, {data: reservation});
  }

  delete(id: any) {
    const params = new HttpParams().set('id', id.toString());
    return this.http.delete(`${this.baseUrl}/delete`, {params: params});
  }

}