import {Author} from "./author";
import {BookMetadata} from "./bookMetadata";

export type Book = {
  id: number;
  gut_id: number;

  metadata: BookMetadata,

  alreadyIndex: boolean;
}
