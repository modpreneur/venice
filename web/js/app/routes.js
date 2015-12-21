/**
 * Created by fisa on 7/23/15.
 */

export default [
    {
        path: '/admin/product/new/free(/)',
        action: 'Product.newFree'
    },
    {
        path: '/admin/product/new/standard(/)',
        action: 'Product.newStandard'
    },
    {
        path: '/admin/blogArticle/tabs/:id(/)',
        action: 'BlogArticle.tabs'
    },
    {
        path: '/admin/blogArticle/new(/)',
        action: 'BlogArticle.new'
    },
];

