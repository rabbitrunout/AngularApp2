import { Injectable } from "@angular/core";
import { HttpClient, HttpHeaders, HttpParams } from "@angular/common/http";
import { map, Observable } from "rxjs";
import { BookingItem } from "./bookingItem";

@Injectable({
  providedIn: 'root'
})
export class BookingService {
  baseUrl = 'http://localhost/angularapp2/bookingapi';


  constructor(private http: HttpClient) {

  }

  getAll() {
    return this.http.get(`${this.baseUrl}/list.php`).pipe(
      map ((res: any) => {
        return res['data'];
      })
    );
  }

  add(reservation: BookingItem) {
    return this.http.post(`${this.baseUrl}/add`,  reservation).pipe(
      map((res: any) => {
        return res['data'];
      })
    );
  }

  edit(reservation: BookingItem) {
    return this.http.put(`${this.baseUrl}/edit`,  reservation);
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
