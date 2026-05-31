class Viewer {
  constructor() {
    this.dir = localStorage.getItem('dir') ?? '2024 웰컴대학로';
    this.$view = $(".cube")[0];
    this.viewPos = { x: 0, y: 0 };
    this.isMouseDown = false;
    this.isFullscreen = false;
    this.interval = null;

    this.init();
    this.setEvents();
  }

  async init() {
    const dirEncoded = encodeURIComponent(this.dir);
    const sides = ['front', 'back', 'left', 'right', 'top', 'bottom'];
    sides.forEach(side => {
      $(`.cube-${side}`).css('background-image', `url(./파노라마/${dirEncoded}/${side}.jpg)`);
    });

    this.$view.style.scale = 1;

    // rendering canvas
    const map = await fetch('./JSON/map.json').then(res => res.json());

    const $canvas = $('canvas')[0];
    const ctx = $canvas.getContext('2d');

    // Find the min and max latitude and longitude
    const longitudes = map.flatMap(({ map }) => map.flat()).map(([val]) => val);
    const latitudes = map.flatMap(({ map }) => map.flat()).map(([_, val]) => val);
    const maxLat = Math.max(...latitudes);
    const minLat = Math.min(...latitudes);
    const maxlon = Math.max(...longitudes);
    const minlon = Math.min(...longitudes);
    const latRange = maxLat - minLat;
    const lonRange = maxlon - minlon;

    // Set canvas dimensions
    $canvas.height = latRange * 130;
    $canvas.width = lonRange * 110;

    // Set static properties for Border classes
    Border.canvas = $canvas;
    Border.maxLat = maxLat;
    Border.minLat = minLat;
    Border.maxlon = maxlon;
    Border.minlon = minlon;
    Border.latRange = latRange;
    Border.lonRange = lonRange;
    const avaliableFestivalNames = ['2025 카운트다운&해맞이 축제', '제51회신라문화제', '2024 웰컴대학로', '장보고수산물축제', '계룡산산신제'];
    const avaliableFestivals = await fetch('./JSON/festivals.json').then(res => res.json()).then((data) => data.filter(({ name }) => avaliableFestivalNames.includes(name)));
    const states = map.map(state => new State(state));

    states.forEach((state) => state.strokeCities(ctx));

    this.renderMap = () => {
      $('.festivalPin').remove();
      avaliableFestivals.forEach(({ name, lng, lat }) => {
        const [x, y] = states[0].lonLatToCanvas(lng, lat);
        const $dot = $(`<div data-festival="${name}" class="festivalPin pointer translate-middle ${name === this.dir ? 'bg-danger' : 'bg-dark'} abs rounded-circle" style="width: 20px; height: 20px; top: ${y}px; left: ${x}px"></div>`)
          .append(`<span class="${name === this.dir ? 'text-bg-danger' : 'text-bg-dark'}  badge rounded-pill abs translate-middle-x start-50 top-100 mt-1" style="white-space: nowrap;">${name}</span>`)
        $('.canvasWrapper').append($dot);
      })
    }

    this.renderMap();
  }

  mousedown() {
    this.isMouseDown = true;
  }

  mousemove({ originalEvent: { movementX, movementY } }) {
    document.body.style.cursor = 'auto';
    if (!this.isMouseDown || this.interval) return;
    document.body.style.cursor = 'move';
    // 10 is a magic number to convert difference to degrees
    this.rotate((movementX) / 10 * -1, (movementY) / 10)
  }

  mouseup() {
    this.isMouseDown = false;
    if (this.interval) {
      clearInterval(this.interval);
      this.interval = null;
    }
  }

  mousewheel({ originalEvent: { deltaY } }) {
    this.zoom(deltaY < 0 ? 0.1 : -0.1);
  }

  keydown(e) {
    if (e.key === 'F11') {
      e.preventDefault();
    }
  }

  ctrlMouseLeave() {
    clearInterval(this.interval);
    this.isMouseDown = false;
  }

  rotate(dx, dy) {
    const iz = 1 / +this.$view.style.scale;
    this.viewPos.x += dx * iz;
    this.viewPos.y = Math.max(-90, Math.min(90, this.viewPos.y + dy * iz));
    this.updateViewTransform(this.viewPos.x, this.viewPos.y);
  }

  zoom(delta) {
    const zoom = +this.$view.style.scale + delta;
    if (zoom < 0.7 || zoom > 3) return;
    this.$view.style.scale = zoom;
    $('.zoomLvl').text(zoom.toFixed(1) + '배');
  }

  updateViewTransform(x, y) {
    this.$view.style.transform = `translateZ(480px) rotateX(${y}deg) rotateY(${x}deg)`;
  }

  fullscreen() {
    (window.innerHeight === screen.height && window.innerWidth === screen.width)
      ? document.exitFullscreen()
      : document.body.requestFullscreen();
    this.isFullscreen = !this.isFullscreen;
  }

  festivalPinClick({ currentTarget: { dataset: { festival } } }) {
    this.dir = festival;
    localStorage.setItem('dir', this.dir);
    this.renderMap();
    const dirEncoded = encodeURIComponent(this.dir);
    const sides = ['front', 'back', 'left', 'right', 'top', 'bottom'];
    sides.forEach(side => {
      $(`.cube-${side}`).css('background-image', `url(./파노라마/${dirEncoded}/${side}.jpg)`);
    });
  }

  setEvents() {
    $(document)
      .on('keydown', this.keydown.bind(this))
      .on('mouseup', this.mouseup.bind(this))
      .on('mousedown', '.view', this.mousedown.bind(this))
      .on('mousemove', '.view', this.mousemove.bind(this))
      .on('mousewheel', '.view', this.mousewheel.bind(this))
      .on('click', '.festivalPin', this.festivalPinClick.bind(this))

    $('.ctrls')
      .on('mouseleave', this.ctrlMouseLeave.bind(this))
      .on('mousedown', '.left', () => this.interval = setInterval(this.rotate.bind(this, -0.5, 0), 16))
      .on('mousedown', '.right', () => this.interval = setInterval(this.rotate.bind(this, 0.5, 0), 16))
      .on('mousedown', '.up', () => this.interval = setInterval(this.rotate.bind(this, 0, 0.5), 16))
      .on('mousedown', '.down', () => this.interval = setInterval(this.rotate.bind(this, 0, -0.5), 16))
      .on('mousedown', '.zoomIn', () => this.interval = setInterval(this.zoom.bind(this, 0.1), 20))
      .on('mousedown', '.zoomOut', () => this.interval = setInterval(this.zoom.bind(this, -0.1), 20))
      .on('click', '.fullscreen', this.fullscreen.bind(this));
  }
}

new Viewer();
