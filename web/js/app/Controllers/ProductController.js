/**
 * Created by Jakub on 10.12.15.
 */


import events from 'trinity/utils/closureEvents';
import Controller from 'trinity/Controller';
import TrinityForm from 'trinity/TrinityForm';
import TrinityTab from 'trinity/TrinityTab';

export default class ProductController extends Controller {
    /**
     * New free product action
     * @param $scope
     */
    newFreeAction($scope) {
        console.log("hi");
        //Attach TrinityForm
        $scope.form = new TrinityForm(q('form[name="freeproducttype"]'), TrinityForm.formType.NEW);
    }

    /**
     * New standard product action
     * @param $scope
     */
    newStandardAction($scope) {
        //Attach TrinityForm
        $scope.form = new TrinityForm(q('form[name="standardproducttype"]'), TrinityForm.formType.NEW);
    }
}