<?php

namespace PromotedProduct;

class Plugin {
    public static function init() {
        Settings::init();
        ProductEditor::init();
    }
}