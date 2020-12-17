<?php


class Mylerz_Shipment_Adminhtml_NeighborhoodsController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        //return Mage::getSingleton('admin/session')->isAllowed('mylerz_shipment/neighborhoods');
        return true;
    }

    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu("cms/mylerzneighborhood")->_addBreadcrumb(Mage::helper("adminhtml")->__("Neighborhoods  Manager"), Mage::helper("adminhtml")->__("Neighborhoods Manager"));
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__("Mylerz"));
        $this->_title($this->__("Manager Neighborhoods"));

        $this->_initAction();
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_title($this->__("Mylerz Neighborhoods"));
        $this->_title($this->__("Edit Mylerz Neighborhoods"));

        $id = $this->getRequest()->getParam("id");
        $model = Mage::getModel("mylerz_shipment/mylerzneighborhood")->load($id);
        if ($model->getId()) {
            Mage::register("mylerzneighborhood_data", $model);
            $this->loadLayout();
            $this->_setActiveMenu("cms/mylerzneighborhood");
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Mylerz Neighborhoods Manager"), Mage::helper("adminhtml")->__("Mylerz Neighborhoods Manager"));
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Mylerz Neighborhoods Description"), Mage::helper("adminhtml")->__("Mylerz Neighborhoods Description"));
            $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock("mylerz_shipment/adminhtml_neighborhoods_edit"))->_addLeft($this->getLayout()->createBlock("mylerz_shipment/adminhtml_neighborhoods_edit_tabs"));
            $this->renderLayout();
        } else {
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("mylerz_shipment")->__("Mylerz Neighborhoods does not exist."));
            $this->_redirect("*/*/");
        }
    }

    public function newAction()
    {
        $this->_title($this->__("Mylerz Neighborhoods"));
        $this->_title($this->__("New Mylerz Neighborhoods"));

        $id = $this->getRequest()->getParam("id");
        $model = Mage::getModel("mylerz_shipment/mylerzneighborhood")->load($id);

        $data = Mage::getSingleton("adminhtml/session")->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register("mylerzneighborhood_data", $model);

        $this->loadLayout();
        $this->_setActiveMenu("cms/mylerzneighborhood");

        $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Mylerz Neighborhoods Manager"), Mage::helper("adminhtml")->__("Mylerz Neighborhoods Manager"));
        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Mylerz Neighborhoods Description"), Mage::helper("adminhtml")->__("Mylerz Neighborhoods Description"));
        $this->_addContent($this->getLayout()->createBlock("mylerz_shipment/adminhtml_neighborhoods_edit"))->_addLeft($this->getLayout()->createBlock("mylerz_shipment/adminhtml_neighborhoods_edit_tabs"));
        $this->renderLayout();

    }

    public function saveAction()
    {

        $post_data = $this->getRequest()->getPost();

        if ($post_data) {
            $_id = $this->getRequest()->getParam("mylerz_neighborhood_id");
            $post_data["mylerz_country_id"] = "EG";

            try {
                $post_data["mylerz_neighborhood_name_id"] = (empty($post_data["mylerz_neighborhood_name_id"])) ? null : $post_data["mylerz_neighborhood_name_id"];

                if (empty($this->getRequest()->getParam("mylerz_neighborhood_id"))) {
                    $model = Mage::getModel("mylerz_shipment/mylerzneighborhood")
                        ->addData($post_data)
                        ->save();
                } else {
                    $model = Mage::getModel("mylerz_shipment/mylerzneighborhood")->load($_id)
                        ->addData($post_data)
                        ->save();
                }

                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Mylerz Neighborhood was successfully saved"));
                Mage::getSingleton("adminhtml/session")->setMylerzneighborhoodData(false);

                if ($this->getRequest()->getParam("back")) {
                    $this->_redirect("*/*/edit", array("id" => $_id));
                    return;
                }
                $this->_redirect("*/*/");
                return;
            } catch (Exception $e) {
                Mage::helper("stylisheve_core")->logException('MylerzNeighborhoods.log',
                    ['exceptionObj' => $e, 'className' => 'Mylerz_Shipment_Adminhtml_NeighborhoodsController', 'methodName' => 'saveAction']
                );
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                Mage::getSingleton("adminhtml/session")->setMylerzneighborhoodData($post_data);
                $this->_redirect("*/*/edit", array("id" => $_id));
                return;
            }
        }
        $this->_redirect("*/*/");
    }

    /**
     *
     *  get id for check the form is edit or new,
     *  return RedirectArg
     *
     * @param $pId
     * @return Array
     */
    public function _getRedirectArg($pId)
    {
        if ($pId) {
            return ['url' => "*/*/edit", 'data' => ["id" => $pId]];
        } else {
            return ['url' => "*/*/new", 'data' => []];
        }
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam("id") > 0) {
            try {
                $model = Mage::getModel("mylerz_shipment/mylerzneighborhood");
                $model->setId($this->getRequest()->getParam("id"))->delete();
                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Mylerz Neighborhood was successfully deleted"));
                $this->_redirect("*/*/");
            } catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
            }
        }
        $this->_redirect("*/*/");
    }
}
