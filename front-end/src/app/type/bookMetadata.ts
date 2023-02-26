import {Author} from "./author";

export type BookMetadata = {
  id: number;
  title: string;
  authors: Author[],
  translators: any[],
  subjects: string[],
  bookShelves: any[],
  languages: string[],
  copyright: boolean,
  mediaType: string,
  formats: {
    [key: string]: string
  },
  downloadCount: number
}
