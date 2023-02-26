import {Component, Input, OnInit} from '@angular/core';
import {Book} from "../../type/book";

@Component({
  selector: 'app-book',
  templateUrl: './book.component.html',
  styleUrls: ['./book.component.scss']
})
export class BookComponent implements OnInit {

  @Input() book: Book | undefined;
  urls: {
    [key: string]: string
  } = {}

  constructor() {
  }

  ngOnInit(): void {
    if (this.book?.metadata?.formats) {
      if ("text/html" in this.book.metadata.formats) {
        this.urls["text/html"] = this.book.metadata.formats["text/html"]
      }
      if ("text/plain" in this.book.metadata.formats) {
        this.urls["text/plain"] = this.book.metadata.formats["text/plain"]
      }
      if("application/epub+zip" in this.book.metadata.formats){
        this.urls["application/epub+zip"] = this.book.metadata.formats["application/epub+zip"]
      }
    }
  }

}
