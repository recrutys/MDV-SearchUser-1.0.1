<?php

namespace MDV\SearchUser\XF\Pub\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\Error;
use XF\Mvc\Reply\View;
use XF\Mvc\Reply\Message;

class Search extends XFCP_Search
{
    /**
     * @return \XF\Mvc\Reply\AbstractReply|\XF\Mvc\Reply\Error|Message|\XF\Mvc\Reply\Redirect
     */
    public function actionSearch()
    {
        $search = parent::actionSearch();

        if ($search instanceof Message)
        {
            $words = $this->filter('keywords', 'str');
            $search = $this->view('MDV:SearchUser\NotFound', 'message_page', ["message" => \XF::phrase("no_results_found")]);

            $user = $this->finder('XF:User');
            $foundUsers = $user->where('username', 'LIKE', $user->escapeLike($words, '%?%'))
                ->fetch();

            $search->setParam('foundUsers', $foundUsers);
        }

        return $search;
    }

    /**
     * @param ParameterBag $params
     * @return mixed|\XF\Mvc\Reply\AbstractReply|Message|\XF\Mvc\Reply\Redirect|View
     */
    public function actionResults(ParameterBag $params)
    {
        $search = parent::actionResults($params);

        if ($search instanceof View)
        {
            $searchWords = $search->getParam('search');

            $words = $searchWords->search_query;
            $user = $this->finder('XF:User');

            $foundUsers = $user->where('username', 'LIKE', $user->escapeLike($words, '%?%'))
                ->fetch(\XF::options()->mdv_searchUser_limit);

            $search->setParam('foundUsers', $foundUsers);
        }

        return $search;
    }
}
