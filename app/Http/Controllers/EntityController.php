<?php

namespace App\Http\Controllers;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\LeadModel;
use App\Models\AmoApiClient;
use App\Models\AmoToken;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use League\OAuth2\Client\Token\AccessToken;

class EntityController extends Controller
{
    public function load() {
        $data=AmoToken::all();
        $apiClient=AmoApiClient::get()->setAccessToken(new AccessToken([
            'access_token' => $data[0]['access_token'],
            'expires' => $data[0]['expires'],
        ]));
        $leads = $apiClient->leads()->get(null,[LeadModel::CONTACTS])/*->getOne(4204299, [LeadModel::CONTACTS])*/;
        $leadsColl = new LeadsCollection();
        foreach ($leads as $lead) {
            $contcoll = new ContactsCollection();
            foreach ($lead->getContacts()->toArray() as $contact) {
                $cont = $apiClient->contacts()->syncOne($lead->getContacts()->getBy('id', $contact['id']));
                if($cont->getCompany())
                $cont->setCompany($apiClient->companies()->getOne($cont->getCompany()->toArray()['id'], [LeadModel::CONTACTS]));
                $contcoll->add($cont);
            }
            $lead->setContacts($contcoll);
            $com = $apiClient->companies()->getOne($lead->getCompany()->toArray()['id'], [LeadModel::CONTACTS]);
            $contcollcom = new ContactsCollection();
            if($com->getContacts())
            foreach ($com->getContacts()->toArray() as $contact) {
                $contcollcom->add($apiClient->contacts()->getOne($contact['id']));
            }
            $com->setContacts($contcollcom);
            $lead->setCompany($com);
            $entity=Lead::query()->whereJsonContains('data_lead->id',$lead->toArray()['id'])->get();
            if(count($entity->toArray())==0){
            $newEntity = new Lead();
            $newEntity->data_lead = $lead->toArray();
            $newEntity->save();
            } else {
                Lead::query()->whereJsonContains('data_lead->id',$lead->toArray()['id'])->update(['data_lead' => $lead->toArray()]);
            }
            $leadsColl->add($lead);
        }

        dd($leadsColl);

    }
}
