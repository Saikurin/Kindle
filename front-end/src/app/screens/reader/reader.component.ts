import {Component, OnInit} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {ActivatedRoute} from "@angular/router";
import {environment} from "../../../environments/environment";
import {Book} from "../../type/book";
import {Obj} from "@popperjs/core";
import {BookMetadata} from "../../type/bookMetadata";
import ePub, {Rendition} from 'epubjs'

@Component({
  selector: 'app-reader',
  templateUrl: './reader.component.html',
  styleUrls: ['./reader.component.scss']
})
export class ReaderComponent implements OnInit {
  public book: any | undefined;
  public leftRendition: Rendition | undefined;
  public rightRendition: Rendition | undefined;
  private totalPages: number = 0;
  private leftIndex: number = 0;
  private rightIndex: number = 1;

  constructor(private httpService: HttpClient, private activatedRoute: ActivatedRoute) {
  }

  ngOnInit(): void {
    this.activatedRoute.params.subscribe(params => {
      this.httpService.get<BookMetadata>(environment.api_url + '/books/' + params['id']).subscribe(book => {
        this.book = ePub('https://www.gutenberg.org/cache/epub/' + book.id + '/pg' + book.id + '-images-3.epub');

        this.leftRendition = this.book.renderTo("left-page", {
          width: "100%",
          height: "100%",
          layout: "single",
          flow: "scrolled-doc"
        });
        this.rightRendition = this.book.renderTo("right-page", {
          width: "100%",
          height: "100%",
          layout: "single",
          flow: "scrolled-doc",
        });

        if (this.leftRendition && this.rightRendition) {

          this.book.ready.then(() => {
            const spine = this.book.spine;
            this.totalPages = spine.length;

            this.leftRendition?.display(this.leftIndex);
            this.rightRendition?.display(this.rightIndex);
          });
        }
      });
    });
  }

  nextPage() {
    if (this.rightIndex < this.totalPages) {
      if(this.leftIndex === 0) {
        this.rightRendition?.next();
      }
      this.leftIndex++;
      this.rightIndex++;
      this.leftRendition?.next();
      this.leftRendition?.next();
      this.rightRendition?.next();
      this.rightRendition?.next();
    }

  }

  prevPage() {
    if (this.leftIndex > 0) {
      this.leftIndex--;
      this.rightIndex--;
      this.leftRendition?.display(this.book.spine.get(this.leftIndex));
      this.rightRendition?.display(this.book.spine.get(this.rightIndex));
    }
  }
}
