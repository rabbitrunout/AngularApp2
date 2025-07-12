export interface BookingItem {
  ID?: number;
  location: string;
  start_time: string;
  end_time: string;
  imageName?: string;
  complete: boolean;
}
