webpackJsonp([9],{

/***/ 1866:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

var _wp = wp,
    _wp$element = _wp.element,
    createElement = _wp$element.createElement,
    useEffect = _wp$element.useEffect,
    registerPlugin = _wp.plugins.registerPlugin,
    PluginPostStatusInfo = _wp.editPost.PluginPostStatusInfo,
    _wp$data = _wp.data,
    withSelect = _wp$data.withSelect,
    withDispatch = _wp$data.withDispatch,
    compose = _wp.compose.compose;

// The array/object key that will be sent with the REST request

var key = 'isGutenbergPost';

var PluginIsGutenbergPost = function PluginIsGutenbergPost(_ref) {
    var setIsGutenbergPost = _ref.setIsGutenbergPost,
        isDirty = _ref.isDirty;

    useEffect(function () {
        setIsGutenbergPost();
    }, [isDirty]);
    return React.createElement(
        PluginPostStatusInfo,
        null,
        null
    );
};

var EnhancedIsGutenbergPost = compose([withSelect(function (select) {
    return {
        isDirty: true
    };
}), withDispatch(function (dispatch, _, _ref2) {
    var select = _ref2.select;

    return {
        setIsGutenbergPost: function setIsGutenbergPost() {
            var isDirty = select('core/editor').isEditedPostDirty();
            var isGBPost = select('core/editor').getEditedPostAttribute(key) || false;
            if (!isGBPost && isDirty) {
                dispatch('core/editor').editPost(_defineProperty({}, key, true), { undoIgnore: true });
            }
        }
    };
})])(PluginIsGutenbergPost);

registerPlugin('is-gutenberg-post', { render: EnhancedIsGutenbergPost });

/***/ })

},[1866]);