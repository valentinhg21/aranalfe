const gulp = require("gulp");
const concat = require("gulp-concat");
const uglify = require("gulp-uglify");
const rename = require("gulp-rename");
const sass = require("gulp-sass")(require("sass"));
const sourcemaps = require("gulp-sourcemaps");
const autoprefixer = require("gulp-autoprefixer");
const stripCssComments = require("gulp-strip-css-comments");
const browserSync = require("browser-sync");
const replace = require("gulp-string-replace");
const imagemin = require("gulp-imagemin");
const imageminPngquant = require("imagemin-pngquant");
const copydir = require("copy-dir");
const mode = require("gulp-mode")();
const webp = require("gulp-webp");
const path = require("path");
// CREAR BLOQUE
const prompt = require("gulp-prompt");
const through2 = require("through2");
const fs = require("fs");
const file = require("gulp-file");
// Create browserSync server
var server = browserSync.create();
// Set paths for dest and source files
var paths = {
  dest: "./dist",
  styles: {
    src: "./assets/sass/**/*.scss",
    sourcemaps: "./dist/css/sourcemap.css",
  },
  block_styles: {
    src: "./assets/sass/gutemberg/**/*.scss",
    sourcemaps: "./dist/css/block.sourcemap.css",
  },
  scripts: {
    src: [
      "./assets/js/utils/*.js",
      "./assets/js/index.js",
      "./assets/js/woocommerce/*.js",
    ],
  },
  images: {
    src: [
      "./assets/img/**/*.jpg",
      "./assets/img/**/*.jpeg",
      "./assets/img/**/*.png",
      "./assets/img/**/*.svg",
      "./assets/img/**/*.gif",
      "./assets/img/**/*.webp",
      "./assets/img/**/*.ico",
      "./assets/fav/**/*.mp4",
    ],
    dest: "./dist/img",
  },
  fav: {
    src: [
      "./assets/fav/**/*.jpg",
      "./assets/fav/**/*.jpeg",
      "./assets/fav/**/*.png",
      "./assets/fav/**/*.svg",
      "./assets/fav/**/*.gif",
      "./assets/fav/**/*.webp",
      "./assets/fav/**/*.ico",
    ],
    dest: "./dist/fav",
  },
  video: {
    src: ["./assets/video/**/*.mp4"],
    dest: "./dist/video",
  },
  fonts: {
    src: [
      "./assets/webfonts/*.ttf",
      "./assets/webfonts/*.woff",
      "./assets/webfonts/*.woff2",
      "./assets/webfonts/*.eot",
      "./assets/webfonts/*.svg",
    ],
    dest: "./dist/webfonts",
  },
};
// Options
var sassOptions = {
  errLogToConsole: true,
  /*
    Output options:
    - compressed
    - compact
    - nested
    - expanded
    */
  outputStyle: "compressed",
};
// Uglify and concat JS files
function scripts() {
  return gulp
    .src(paths.scripts.src[1])
    .pipe(uglify())
    .pipe(concat("index.min.js"))
    .pipe(gulp.dest(paths.dest + "/js"))
    .pipe(server.stream());
}
function utilsScript() {
  return gulp
    .src(paths.scripts.src[0])
    .pipe(gulp.dest(paths.dest + "/js/utils"))
    .pipe(server.stream());
}
function woocommerce() {
  return gulp
    .src(paths.scripts.src[2])
    .pipe(gulp.dest(paths.dest + "/js/woocommerce"))
    .pipe(server.stream());
}
// Bundle styles files
function styles() {
  // Bundle everything
  return gulp
    .src(paths.styles.src)
    .pipe(mode.development(sourcemaps.init()))
    .pipe(sass(sassOptions).on("error", sass.logError))
    .pipe(autoprefixer())
    .pipe(
      rename({
        suffix: ".min",
      })
    )
    .pipe(
      stripCssComments({
        preserve: false,
      })
    )
    .pipe(mode.development(sourcemaps.write("./sourcemaps")))
    .pipe(gulp.dest(paths.dest + "/css"))
    .pipe(server.stream());
}

// Bundle styles files
function block_styles() {
  // Bundle everything
  return gulp
    .src(paths.block_styles.src)
    .pipe(mode.development(sourcemaps.init()))
    .pipe(sass(sassOptions).on("error", sass.logError))
    .pipe(autoprefixer())
    .pipe(
      rename({
        suffix: ".min",
      })
    )

    .pipe(
      stripCssComments({
        preserve: false,
      })
    )
    .pipe(mode.development(sourcemaps.write("./sourcemaps")))
    .pipe(gulp.dest(paths.dest + "/blocks-styles"))
    .pipe(server.stream());
}
// Reload when required
function reload(done) {
  server.reload();
  done();
}
// Serve the app using browserSync
function serve(done) {
  server.init({
    proxy: "",
    port: 8888,
  });

  done();
}

function images_compression(done) {
  return gulp
    .src(paths.images.src)
    .pipe(
      imagemin([
        imagemin.mozjpeg({
          quality: 60,

          progressive: true,
        }),

        imageminPngquant({ quality: [0.1, 0.2] }),
      ])
    )

    .pipe(gulp.dest(paths.images.dest));
}

function convertWebp(done) {
  return gulp
    .src(paths.images.src)
    .pipe(webp({ quality: 40 }))
    .pipe(gulp.dest(paths.images.dest));
}

function copy_fonts() {
  return gulp.src(paths.fonts.src).pipe(gulp.dest(paths.fonts.dest));
}

function copy_fav() {
  return gulp.src(paths.fav.src).pipe(gulp.dest(paths.fav.dest));
}

function copy_video() {
  return gulp.src(paths.video.src).pipe(gulp.dest(paths.video.dest));
}

// Watch and reload on changes

function watch() {
  gulp.watch(paths.scripts.src, gulp.series(scripts, reload));
  gulp.watch(paths.scripts.src, gulp.series(utilsScript, reload));
  gulp.watch(paths.scripts.src, gulp.series(woocommerce, reload));
  gulp.watch(paths.styles.src, gulp.series(styles, reload));
  gulp.watch(paths.images.src, gulp.series(convertWebp, reload));
  gulp.watch(paths.images.src, gulp.series(images_compression, reload));
  gulp.watch(paths.block_styles.src, gulp.series(block_styles, reload));
}

// Compress images
gulp.task("compress-images", gulp.series(images_compression));
gulp.task("compress-webp", gulp.series(convertWebp));
gulp.task("copyfonts", function () {
  copy_fonts;
});
gulp.task("copyfav", function () {
  copy_fav;
});
gulp.task("copyvideo", function () {
  copy_video;
});
// Build for prod
gulp.task(
  "build",
  gulp.series(
    styles,
    utilsScript,
    scripts,
    woocommerce,
    images_compression,
    convertWebp,
    copy_fonts,
    copy_fav,
    copy_video,
    serve,
    watch
  )
);
gulp.task("block", gulp.series(block_styles, serve, watch));
// When starting, initialize styles, scripts, serve and watch files
gulp.task(
  "default",
  gulp.series(
    styles,
    utilsScript,
    scripts,
    woocommerce,
    images_compression,
    convertWebp,
    copy_fonts,
    copy_fav,
    copy_video,
    serve,
    watch
  )
);

gulp.task("create", function () {
  let blockName = "";
  return gulp
    .src("templates-block/block/**/*")
    .pipe(
      prompt.prompt(
        {
          type: "input",
          name: "name",
          message: "Nombre del bloque (ej: portada):",
        },
        function (res) {
          blockName = res.name.toLowerCase();
          const blockPath = path.join(__dirname, "blocks", blockName);

          if (fs.existsSync(blockPath)) {
            console.log(`❌ El bloque "${blockName}" ya existe.`);
            process.exit(1);
          }
        }
      )
    )
    .pipe(
      through2.obj(function (file, _, cb) {
        file.blockName = blockName;
        cb(null, file);
      })
    )
    .pipe(replace("acf/bloque", () => `acf/${blockName}`))
    .pipe(replace(/Bloque/g, () => capitalize(blockName)))
    .pipe(replace(/bloque/g, () => blockName))
    .pipe(
      rename(function (path) {
        path.basename = path.basename.replace("bloque", blockName);
      })
    )
    .pipe(gulp.dest(() => `blocks/${blockName}`))
    .on("end", () => {
      const scssContent = `.block-${blockName} {
      @import '../variables/variables';
      @import '../components/media';
      @import '../components/colors';
      @import '../components/fonts';
    }`;
      file(`${blockName}.scss`, scssContent, { src: true }).pipe(
        gulp.dest("assets/sass/gutemberg/")
      );
    });
});

function capitalize(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}
