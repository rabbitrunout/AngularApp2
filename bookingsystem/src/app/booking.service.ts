import { Injectable } from "@angular/core";
import { HttpClient, HttpParams } from "@angular/common/http";
import { map, Observable } from "rxjs";
import { BookingItem } from "./bookingItem";

@Injectable({ providedIn: 'root' })
export class BookingService {

  baseUrl = 'http://localhost/angularapp2/bookingapi';

  constructor(private http: HttpClient) {}

  getAll() {
    return this.http.get(`${this.baseUrl}/list.php`).pipe(
      map((res: any) => res.data)
    );
  }

  add(reservation: BookingItem): Observable<BookingItem> {
  return this.http.post<BookingItem>('http://localhost/angularapp2/bookingapi/add.php', reservation);
}

  edit(reservation: BookingItem) {
  return this.http.post('http://localhost/angularapp2/bookingapi/edit.php', reservation);
}

  delete(ID: number) {
    const params = new HttpParams().set('ID', ID.toString());
    return this.http.delete(`${this.baseUrl}/delete.php`, { params });
  }

  uploadFile(file: File) {
    const formData = new FormData();
    formData.append('image', file);
    return this.http.post(`${this.baseUrl}/upload`, formData);
  }
}
