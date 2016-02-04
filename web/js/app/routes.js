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
        path: '/admin/product/tabs/:id(/)',
        action: 'Product.tabs'
    },
    {
        path: '/admin/product/content-product/tabs/:id',
        action: 'Product.contentProductTabs'
    },
    {
        path: '/admin/blogArticle/tabs/:id(/)',
        action: 'BlogArticle.tabs'
    },
    {
        path: '/admin/blogArticle/new(/)',
        action: 'BlogArticle.new'
    },
    {
        path: '/admin/content/tabs/:id(/)',
        action: 'Content.tabs'
    },
    {
        path: '/admin/content/content-product/tabs/:id',
        action: 'Content.contentProductTabs'
    },
    {
        path: '/admin/content/new/pdf(/)',
        action: 'Content.newPdf'
    },
    {
        path: '/admin/content/new/iframe(/)',
        action: 'Content.newIframe'
    },
    {
        path: '/admin/content/new/mp3(/)',
        action: 'Content.newMp3'
    },
    {
        path: '/admin/content/new/video(/)',
        action: 'Content.newVideo'
    },
    {
        path: '/admin/content/new/html(/)',
        action: 'Content.newHtml'
    },
    {
        path: '/admin/content/new/group(/)',
        action: 'Content.newGroup'
    },
    {
        path: '/admin/content/:id/content-product/new',
        action: 'Content.newContentProduct'
    },
    {
        path: '/admin/product/:id/content-product/new',
        action: 'Product.newContentProduct'
    },
    {
        path: '/admin/user/new(/)',
        action: 'User.new'
    },
    {
        path: '/admin/user/tabs/:id(/)',
        action: 'User.tabs'
    },
    {
        path: '/admin/billing-plan/tabs/:id(/)',
        action: 'BillingPlan.tabs'
    },
    {
        path: '/admin/billing-plan/new/:id(/)',
        action: 'BillingPlan.new'
    },
    {
        path: '/admin/product-access/tabs/:id(/)',
        action: 'ProductAccess.tabs'
    },
    {
        path: '/admin/product-access/new/:id(/)',
        action: 'ProductAccess.new'
    }
];

