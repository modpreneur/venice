/**
 * Created by Jakub Fajkus on 10.12.15.
 */
import events from 'trinity/utils/closureEvents';
import Controller from 'trinity/Controller';
import VeniceForm from '../Libraries/VeniceForm';
import Slugify from '../Libraries/Slugify';
import TrinityTab from 'trinity/components/TrinityTab';

export default class BlogArticleController extends Controller {

    /**
     * Tabs action
     * @param $scope
     */
    tabsAction($scope) {
        //Tell trinity there is tab to be loaded
        $scope.trinityTab = new TrinityTab();

        //On tabs load
        $scope.trinityTab.addListener('tab-load', function (e) {

        if(e.id == "tab2") {
            let form = e.element.q('form');

            $scope.veniceForms = $scope.veniceForms || {};
            $scope.veniceForms[e.id] = new VeniceForm(form);

            let settingsString = q("#blog_article_content").getAttribute("data-settings");
            $("#blog_article_content").froalaEditor(JSON.parse(settingsString));

            this.handleHandleGeneration();
        }
        }, this);
    }

    /**
     * New blog article action
     * @param $scope
     */
    newAction($scope) {
        $scope.form = new VeniceForm(q('form[name="blog_article"]'), VeniceForm.formType.NEW);

        let settingsString = q("#blog_article_content").getAttribute("data-settings");

        $("#blog_article_content").froalaEditor(JSON.parse(settingsString));

        this.handleHandleGeneration();
    }

    handleHandleGeneration() {
        var titleField = q.id('blog_article_title');
        var handleField = q.id('blog_article_handle');

        if (titleField && handleField) {
            events.listen(titleField, 'input', function () {
                Slugify.slugify(titleField, handleField);
            });
        }
    }
}