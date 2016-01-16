/**
 * Created by Jakub Fajkus on 10.12.15.
 */
import events from 'trinity/utils/closureEvents';
import Controller from 'trinity/Controller';
import VeniceForm from '../Libraries/VeniceForm';
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
        $scope.trinityTab.addListener('tab-load', function(e) {
            let form = e.element.q('form');
            if(form){
                $scope.veniceForms = $scope.veniceForms || {};
                $scope.veniceForms[e.id] = new VeniceForm(form);
            }

            // Reload tab1 (SHOW) when success update
            $scope.veniceForms[e.id].success((e)=>{
                $scope.trinityTab.reload('tab1');
            });
        }, this);
    }

    /**
     * New blog article action
     * @param $scope
     */
    newAction($scope) {
        $scope.form = new VeniceForm(q('form[name="blog_article"]'), VeniceForm.formType.NEW);
    }
}