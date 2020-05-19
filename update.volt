
{{ content() }}

<form action="" method="post" multipart="" enctype="multipart/form-data">
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="inputEmail4">Book title</label>
      <input type="text" name="bookname" class="form-control" id="inputBook" placeholder="Book title" value="{{book.bookname}}">
      <label for="inputAddress">Author title</label>
      <input type="text" name="authorname" class="form-control" id="inputAuthor" placeholder="Author title" value="{{book.authorname}}">
      <label for="inputAddress2">Publisher</label>
      <input type="text" class="form-control" name="publisher" id="inputPublisher" placeholder="Publisher title" value="{{book.publisher}}">
      <label for="inputCity">Category</label>
      <input type="text" class="form-control" name="category" id="inputCategory" placeholder="Category title" value="{{book.category}}">
      <label for="exampleFormControlFile1">Example file input</label>
      <input type="file" class="btn btn-primary" id="img" name="img">
      <br />
      <p>
        <button type="submit" name="submit" class="btn btn-primary">Change book data</button>
      </p>
    </div>
  </div>
</form>