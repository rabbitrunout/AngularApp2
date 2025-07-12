import { Injectable } from "@angular/core";
import { HttpClient, HttpHeaders, HttpParams } from "@angular/common/http";
import { map, Observable } from "rxjs";
import { BookingItem } from "./bookingItem";
import { BookingComponent } from "./bookingcomponent/booking.component";

@Injectable({
  providedIn: 'root'
})
export class BookingService {
  baseUrl = 'http://localhost/angularapp2-1/bookingapi';


  constructor(private http: HttpClient) {}

  getAll() {
    return this.http.get(`${this.baseUrl}/list.php`).pipe(
      map ((res: any) => {
        return res['data'];
      })
    );
  }

  add(reservation: BookingItem) {
    return this.http.post(`${this.baseUrl}/add.php`, {data: reservation}).pipe(
      map((res: any) => {
        return res['data'];
      })
    );
  }

  edit(reservation: BookingItem) {
    return this.http.put(`${this.baseUrl}/edit`, {data: reservation});
  }

  delete(ID: any) {
    const params = new HttpParams().set('ID', ID.toString());
    return this.http.delete(`${this.baseUrl}/delete`, { params });
  }

  uploadFile(file: File) {
  const formData = new FormData();
  formData.append('image', file);  // ключ 'image' для PHP
  return this.http.post(`${this.baseUrl}/upload`, formData);
}

}
