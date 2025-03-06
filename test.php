<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Document</title>
</head>

<body>
  <h1>測試 upload-avatar.php API</h1>
  <form name="form1" onsubmit="sendData(event)">
    <input type="file" name="avatar" accept="image/png,image/jpeg" />
    <br />
    <button>上傳</button>
  </form>
  <img src="" alt="" id="my_img" width="300" />
  <script>
    const sendData = (e) => {
      e.preventDefault();

      const fd = new FormData(document.form1);

      fetch("./upload-avatar.php", {
          method: "POST",
          body: fd,
        })
        .then((r) => r.json())
        .then((obj) => {
          console.log(obj);
          if (obj.success && obj.file) {
            my_img.src = `../images/${obj.file}`;
          }
        });

      setTimeout(() => {
        console.log("等五秒")
      }, 5000);
    };
  </script>
</body>

</html>