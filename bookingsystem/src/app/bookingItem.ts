export interface BookingItem {
  ID?: number;
  location: string;
  start_time: string;
  end_time: string;
  complete: boolean;
  imageName?: string;
}
