
{{ content() }}

{{ flashSession.output() }}

<br />
<form action="" method="post" multipart="" enctype="multipart/form-data">
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="inputEmail4">Book title</label>
      <input type="text" name="bookname" class="form-control" id="inputBook" placeholder="Book title">
      <label for="inputAddress">Author title</label>
      <input type="text" name="authorname" class="form-control" id="inputAuthor" placeholder="Author title">
      <label for="inputAddress2">Publisher</label>
      <input type="text" class="form-control" name="publisher" id="inputPublisher" placeholder="Publisher title">
      <label for="inputCity">Category</label>
      <input type="text" class="form-control" name="category" id="inputCategory" placeholder="Category title">
      <label for="exampleFormControlFile1">Example file input</label>
      <input type="file" class="btn btn-primary" id="img" name="img">
      <br />
      <p>
        <button type="submit" name="submit" class="btn btn-primary">Insert new book</button>
        <a href="/book/index/" class="btn btn-success">Back book information</a>
      </p>
    </div>
  </div>
</form>