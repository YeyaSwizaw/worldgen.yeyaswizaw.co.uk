#![feature(plugin, decl_macro)]
#![plugin(rocket_codegen)]

extern crate rocket;
extern crate rocket_contrib;
extern crate png;
extern crate serde_json;

use rocket::{
    response::content::Content,
    http::ContentType
};

use rocket_contrib::static_files::StaticFiles;

use png::HasParameters;

#[get("/<x>/<y>")]
fn get_tile(x: i64, y: i64) -> Content<Vec<u8>> {
    let mut buf = Vec::new();

    write_png(&mut buf);

    Content(ContentType::PNG, buf)
}

fn write_png(mut buf: &mut Vec<u8>) {
    let mut encoder = png::Encoder::new(&mut buf, 256, 256);
    encoder
        .set(png::ColorType::RGB)
        .set(png::BitDepth::Eight);

    let mut writer = encoder.write_header().unwrap();

    let mut data = Vec::with_capacity(256 * 256 * 4);
    for y in 0 ..= 255 {
        for x in 0 ..= 255 {
            data.extend_from_slice(&[y, x, 255 - x]);
        }
    }

    writer.write_image_data(&data).unwrap();
}

fn main() {
    rocket::ignite()
        .mount("/tile", routes![get_tile])
        .mount("/", StaticFiles::from("static"))
        .launch();
}
