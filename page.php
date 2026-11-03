<?php

app()['sage.view'] = 'page';

echo view(app('sage.view'), app('sage.data'))->render();
