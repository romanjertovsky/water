<?php


class providers extends ModuleCommon implements iModules
{

    public function getModuleRoles(): array
    {
        return['admin'];
    }

    public function getTitle(): string
    {
        return 'Поставщики';
    }

    public function moduleInit($aModuleParams)
    {
        $this->setParams($aModuleParams);
    }


    protected function getProvidersWithRole(int $iProviderId)
    {// TODO это - костыль переделать на getRecordsUsed()

        $oQuery = new qbSelect('supply_points', '*', ['provider_id' => $iProviderId]);
        $aAssignedProviders = $oQuery->runMakeSelect();

        $aPointsWithThisContract = [];

        foreach ($aAssignedProviders as $key => $val) {

            $aPointsWithThisContract[] =
                'id: ' . $val['point_id'] .
                '; ' . $val['point_name'] .
                //TODO '; Адрес: ' . $val['point_city'] .
                ', ' . $val['point_street'] .
                ', ' . $val['point_house'];

        }

        return $aPointsWithThisContract;

    }


}