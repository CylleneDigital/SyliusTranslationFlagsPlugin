// Front-end entry point for the shop, expected by the test application's webpack.config.js as by
// any Sylius application:
// `addEntry('plugin-shop-entry', '<plugin>/assets/shop/entrypoint.js')`.
//
// This plugin ships no JavaScript, and does not touch the shop at all. The file is nonetheless
// required - without it `yarn build` fails and every admin page answers 500.
