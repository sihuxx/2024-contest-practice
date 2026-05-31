<style>
    .flex-center {padding: 200px 0;}
    .canvasWrapper {position: relative;}
.popup { pointer-events: none; padding: 10px; border-radius: 5px; background-color: rgba(0, 0, 0, 0.7); color: white; position: absolute;}
.popup:empty {transition: all .25s; padding: 0; opacity: 0;}
.popup p {display: flex; align-items: center; gap: 5px; }
.popup p span {width: 1rem; height: 1rem; border-radius: 3px; display: inline-block;}
#circleCanvas {position: absolute; inset: 0; pointer-events: none;}
</style>
  
  <main class="flex-center">  
      <aside class="flex-col gap-3 p-3 bg-white position-fixed z-3 start-0 top-0 border-end transition" style="min-height: 100dvh; max-width: 450px; min-width: 300px; transform: translate(-100%) ">
        <form action="" class="flex gap-3">
          <input type="text" class="flex-1 form-control" name="input">
          <button class="btn-primary btn">검색</button>
        </form>
        <div class="types flex gap-2 mb-5">

        </div>
        <div class="festivals overflow-auto flex-col gap-3" style="height: 750px">
          
        </div>

        <div class="toggle abs start-100 top-50 border border-dark pointer border-start-0 px-2 py-3 rounded-1">sf</div>
      </aside>
      <div class="canvasWrapper">
        <canvas id="mapCanvas"></canvas>
        <canvas id="circleCanvas"></canvas>
        <div class="popup"></div>
      </div>
    </main>

    <script src="./vendor/jquery-3.7.1.js"></script>
    <script src="./js/App.js"></script>
    <script>new App()</script>
  </div>
